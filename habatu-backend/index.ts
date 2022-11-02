import express, { Express, Request, Response, json, } from 'express';
import cors from "cors"
import "express-async-errors";
import { connect, connection } from 'mongoose';
import { router } from './routes/index';
import {Option, User} from './models'
import bcrypt from "bcryptjs"
process.env.TZ ='Europe/Zurich'
const dropDbOnStartUp= process.env.CLEAR_ON_STARTUP=="cleanup"
if(!process.env.TOKEN_KEY){
  process.env.TOKEN_KEY = "supersecret"
}

connect(process.env.NODE_ENV=="production"?"mongodb://mongo:27017":"mongodb://localhost:27018",async ()=>{
if(dropDbOnStartUp){  
connection.db.dropDatabase()
  User.create({
    nickname: process.env.ADMIN_NICKNAME,
    password: await bcrypt.hash(process.env.ADMIN_PASSWORD!, 10),
    role: "Admin"
  })
  Option.create({
    tournamentName: "HaBaTu",
    startedTournament: false,
    endedRoundGames: false,
    additionalSlots: 5
  })
}
});
const app: Express = express();
const port = process.env.PORT;
app.use(json())
app.use(cors({
  origin: process.env.NODE_ENV=="production"?process.env.ALLOWED_HOST:"*"
}))
app.use(router);
app.get('/', (req: Request, res: Response) => {
  res.json({ message: "Alles guet :)" })
});
app.use(function (err: Error, req: Request, res: Response, next: any) {
  console.error(err.stack);
  res.status(500).json({ message: "An Error occured" });
  next()
});
app.listen(process.env.NODE_ENV=="production"?port:8000, () => {
  console.log(`Server is running at http://localhost:${process.env.NODE_ENV=="production"?port:8000}`);
});