import { Schema, model, connect } from "mongoose";
import { IGame } from "../interfaces/game.interface";
import autopopulate from "mongoose-autopopulate";

const schema = new Schema<IGame>({
  teamA: { type: String, ref: "Team", autopopulate: true },
  teamB: { type: String, ref: "Team", autopopulate: true },
  category: {
    type: String,
    ref: "Category",
    autopopulate: true,
  },
  hall: { type: String, ref: "Hall", autopopulate: true },
  timeslot: {
    type: String,
    ref: "Timeslot",
    autopopulate: true,
  },
  type: { type: String },
  pointsTeamA: { type: Number },
  pointsTeamB: { type: Number },
});
schema.plugin(autopopulate);
export const Game = model<IGame>("Game", schema);
