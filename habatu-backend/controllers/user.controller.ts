import { Request, Response } from 'express';
import { createUser as create, getUsers as gets, updateUsers as update, getUser as get, deleteUser as remove, getUserByNickname } from "../services/user.service";
import bcrypt from "bcryptjs"
import jwt, { JwtPayload } from "jsonwebtoken"
import { Role } from '../interfaces/role';

export const createUser = async (req: Request, res: Response) => {
    try {
    
        const encryptedPassword = await bcrypt.hash(req.body.password, 10);
        req.body.password = encryptedPassword
    

        const user = await create(req.body)
    

        const token = jwt.sign(
          { _id: user._id, nickname: user.nickname, roles: user.roles, group: user.group },
          process.env.TOKEN_KEY,
          {
            expiresIn: "12h",
          }
        );
    
        res.status(201).json({_id: user._id, nickname: user.nickname, roles: user.roles, token});
      } catch (err) {
        console.log(err);
      }
   
}
export const loginUser = async (req: Request, res: Response) => {
  const {nickname, password } = req.body
  const user = await getUserByNickname(nickname)
  if(!user){
    res.status(404).json({message: "User doesn't exist in the database"})
    return
  }
  const correctPassword = await bcrypt.compare(password, user.password)
  if(!correctPassword){
    res.status(403).json({message: "User credentials are wrong"})
    return
  }
  const token = jwt.sign(
    { _id: user._id, nickname: user.nickname, roles: user.roles, group: user.group },
    process.env.TOKEN_KEY,
    {
      expiresIn: "12h",
    }
  );
    res.status(201).json({_id: user._id, roles: user.roles, nickname, token});
  
}

export const getMe = async (req: Request, res: Response) => {
      const {token} = req.body
      const tokenUser : JwtPayload = jwt.verify(token,  process.env.TOKEN_KEY) as JwtPayload
      const user = await get(tokenUser._id)
      console.log(user)
      if(user){
        const token = jwt.sign(
          { _id: user._id, nickname: user.nickname, roles: user.roles, team: user.team },
          process.env.TOKEN_KEY,
          {
            expiresIn: "12h",
          }
        );
      res.status(200).json({_id: user._id, roles: user.roles, team: user.team, nickname: user.nickname, token});
    }else{
      res.status(404)
    }

}

export const getUser = async (req: Request, res: Response) => {
    const user = await get(req.params.id)
    res.json(user)
}
export const getUsers = async (req: Request, res: Response) => {
    const users = await gets()
    res.json(users)
}
export const updateUser = async (req: Request, res: Response) => {
    const user = await update(req.params.id, req.body)
    console.log(user)
    res.json(user)
}
export const deleteUser = async (req: Request, res: Response) => {
    const user = await remove(req.params.id)
    res.json(user)
}
