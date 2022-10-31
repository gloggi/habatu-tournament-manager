import express, { Express, Request, Response, json, } from 'express';
import cors from "cors"
import "express-async-errors";
import { connect, connection } from 'mongoose';
import dotenv from 'dotenv';
import { router } from './routes/index';
import {Option, User} from './models'
import bcrypt from "bcryptjs"
process.env.TZ ='Europe/Zurich'
const dropDbOnStartUp= false

dotenv.config();
connect(process.env.NODE_ENV=="production"?"mongodb://mongo:27017":"mongodb://localhost:27018",async ()=>{
if(dropDbOnStartUp){  
connection.db.dropDatabase()
  User.create({
    nickname: "admin",
    password: await bcrypt.hash("Test", 10),
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
  origin: "*"
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
app.listen(port, () => {
  console.log(`Server is running at https://localhost:${port}`);
});