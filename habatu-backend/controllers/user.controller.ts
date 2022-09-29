import { Request, Response } from 'express';
import { createUser as create, getUsers as gets, updateUsers as update, getUser as get, deleteUser as remove } from "../services/user.service";
import bcrypt from "bcryptjs"
import jwt from "jsonwebtoken"

export const createUser = async (req: Request, res: Response) => {
    try {
    
        //Encrypt user password
        const encryptedPassword = await bcrypt.hash(req.body.password, 10);
        req.body.password = encryptedPassword
    
        // Create user in our database
        const user = await create(req.body)
    
        // Create token
        const token = jwt.sign(
          { user_id: user._id, nickname: user.nickname },
          process.env.TOKEN_KEY,
          {
            expiresIn: "12h",
          }
        );
        // save user token
        user.token = token;
    
        // return new user
        res.status(201).json(user);
      } catch (err) {
        console.log(err);
      }
      // Our register logic ends here
   
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
    res.json(user)
}
export const deleteUser = async (req: Request, res: Response) => {
    const user = await remove(req.params.id)
    res.json(user)
}
