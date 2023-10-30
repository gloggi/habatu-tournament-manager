import { Request, Response } from "express";
import {
  createUser as create,
  getUsers as gets,
  updateUsers as update,
  getUser as get,
  deleteUser as remove,
  getUserByNickname,
} from "../services/user.service";
import bcrypt from "bcryptjs";
import jwt, { JwtPayload } from "jsonwebtoken";
import { Role } from "../interfaces/role";

export const createUser = async (req: Request, res: Response) => {
  try {
    const encryptedPassword = await bcrypt.hash(req.body.password, 10);
    req.body.password = encryptedPassword;
    const existingUser = await getUserByNickname(req.body.nickname);
    if (existingUser) {
      res.status(409).json({ message: "Nickname already exists!" });
    }

    const user = await create(req.body);

    const token = jwt.sign(
      { _id: user._id, nickname: user.nickname, role: user.role },
      process.env.TOKEN_KEY as string,
      {
        expiresIn: "12h",
      }
    );

    res
      .status(201)
      .json({ _id: user._id, nickname: user.nickname, role: user.role, token });
  } catch (err) {
    console.log(err);
  }
};
export const loginUser = async (req: Request, res: Response) => {
  const { nickname, password } = req.body;
  const user = await getUserByNickname(nickname);
  if (!user) {
    res.status(404).json({ message: "User doesn't exist in the database" });
    return;
  }
  const correctPassword = await bcrypt.compare(password, user.password);
  if (!correctPassword) {
    res.status(403).json({ message: "User credentials are wrong" });
    return;
  }
  const token = jwt.sign(
    { _id: user._id, nickname: user.nickname, role: user.role },
    process.env.TOKEN_KEY as string,
    {
      expiresIn: "12h",
    }
  );
  res.status(201).json({ _id: user._id, role: user.role, nickname, token });
};

export const getMe = async (req: Request, res: Response) => {
  const { token } = req.body;
  const tokenUser: JwtPayload = jwt.verify(
    token,
    process.env.TOKEN_KEY as string
  ) as JwtPayload;
  const user = await get(tokenUser._id);
  if (user) {
    const token = jwt.sign(
      {
        _id: user._id,
        nickname: user.nickname,
        role: user.role,
        team: user.team,
      },
      process.env.TOKEN_KEY as string,
      {
        expiresIn: "12h",
      }
    );
    res
      .status(200)
      .json({
        _id: user._id,
        role: user.role,
        team: user.team,
        nickname: user.nickname,
        token,
        refereeGames: user.refereeGames,
      });
  } else {
    res.status(404);
  }
};

export const getUser = async (req: Request, res: Response) => {
  const user : any = await get(req.params.id);
  if(user){
    user.password = undefined
  }
  res.json(user);
};
export const getUsers = async (req: Request, res: Response) => {
  const users = await gets();
  res.json(users);
};
export const updateUser = async (req: Request, res: Response) => {
  if(req.body.password){
  const encryptedPassword = await bcrypt.hash(req.body.password, 10);
  req.body.password = encryptedPassword;
}
  const user = await update(req.params.id, req.body);
  res.json(user);
};
export const deleteUser = async (req: Request, res: Response) => {
  const user = await remove(req.params.id);
  res.json(user);
};
