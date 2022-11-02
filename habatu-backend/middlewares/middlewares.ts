import { Request, Response, NextFunction } from 'express';
import jwt, { JwtPayload } from "jsonwebtoken"
import { Role } from '../interfaces/role';

export const adminMiddleware = (req: Request, res: Response, next: NextFunction) => {
    const authHeader = req.headers['authorization']
    const token = authHeader && authHeader.split(' ')[1]
    if (!token) {
        res.sendStatus(401)
        return
    }
    try {
        const user : JwtPayload = jwt.verify(token, process.env.TOKEN_KEY) as JwtPayload
        res.locals.user = user
        if(user.role != Role.Admin){
            res.sendStatus(401).json({message: "Finger wäg!"})
            return
        }
        next()
    } catch (e) {
        throw Error((e as Error).message)
    }

}
export const refereeMiddleware = (req: Request, res: Response, next: NextFunction) => {
    const authHeader = req.headers['authorization']
    const token = authHeader && authHeader.split(' ')[1]
    if (!token) {
        res.sendStatus(401)
        return
    }
    try {
        const user : JwtPayload = jwt.verify(token, process.env.TOKEN_KEY) as JwtPayload
        res.locals.user = user
        if(user.role !=Role.Referee&&user.role!=Role.Admin){
            res.sendStatus(401).json({message: "Finger wäg!"})
            return
        }
        next()
    } catch (e) {
        throw Error((e as Error).message)
    }

}