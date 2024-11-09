import { Schema, model, connect } from "mongoose";
import { IUser } from "../interfaces/user.interface";
import autopopulate from "mongoose-autopopulate";
import { Role } from "../interfaces/role";

const schema = new Schema<IUser>({
  nickname: { type: String, unique: true, required: true },
  password: { type: String },
  role: { type: String },
  team: { type: String, ref: "Team", autopopulate: true },
  refereeGames: [
    { type: String, ref: "Game", autopopulate: false },
  ],
});
schema.plugin(autopopulate);
export const User = model<IUser>("User", schema);
