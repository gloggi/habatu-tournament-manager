import { Schema, model, connect } from "mongoose";
import { IOption } from "../interfaces/option.interface";

const schema = new Schema<IOption>({
  tournamentName: { type: String },
  startTime: { type: String },
  gameDuration: { type: String },
  breakDuration: { type: String },
  additionalSlots: { type: Number },
  startedTournament: { type: Boolean },
  endedRoundGames: { type: Boolean },
});
export const Option = model<IOption>("Option", schema);
