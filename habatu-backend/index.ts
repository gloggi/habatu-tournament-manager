import express, { Express, Request, Response, json } from "express";
import cors from "cors";
import "express-async-errors";
import { connect, connection } from "mongoose";
import { router } from "./routes";
import { Option, User } from "./models";
import bcrypt from "bcryptjs";
import dotenv from 'dotenv';
dotenv.config();
process.env.TZ = "Europe/Zurich";
process.env.CLEAR_ON_STARTUP = ""
const mongoUsername = process.env.MONGO_USERNAME
const mongoPassword = process.env.MONGO_PASSWORD
if (!process.env.TOKEN_KEY) {
  process.env.TOKEN_KEY = "supersecret";
}
var mongo_host = "mongo";
var mongo_port = "27017";
if (process.env.RUN_WITHOUT_DOCKER) {
  mongo_host = "localhost";
  mongo_port = "27018";
}
connect(
`mongodb://${mongoUsername}:${mongoPassword}@${mongo_host}:${mongo_port}/production?authSource=admin`,
  async () => {
    const existingUser = await User.findOne({ nickname: process.env.ADMIN_NICKNAME! });
    if (!existingUser) {
      await User.create({
        nickname: process.env.ADMIN_NICKNAME,
        password: await bcrypt.hash(process.env.ADMIN_PASSWORD!, 10),
        role: "Admin",
      });
    }
    const existingOption = await Option.findOne({ tournamentName: "HaBaTu" });
    if (!existingOption) {
      Option.create({
        tournamentName: "HaBaTu",
        startedTournament: false,
        endedRoundGames: false,
        additionalSlots: 5,
      });
    }
  }
);
const app: Express = express();
const port = process.env.PORT;
app.use(json());
app.use(
  cors({
    origin:
      process.env.NODE_ENV == "production" ? process.env.ALLOWED_HOST : "*",
  })
);
app.use(router);
app.get("/", (req: Request, res: Response) => {
  res.json({ message: "Alles guet :)" });
});
app.use(function (err: Error, req: Request, res: Response, next: any) {
  console.error(err.stack);
  res.status(500).json({ message: "An Error occured" });
  next();
});
app.listen(process.env.NODE_ENV == "production" ? port : 8000, () => {
  console.log(
    `Server is running at http://localhost:${process.env.NODE_ENV == "production" ? port : 8000
    }`
  );
});
