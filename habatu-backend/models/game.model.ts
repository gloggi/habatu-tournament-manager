import { Schema, model, connect } from 'mongoose';
import {IGame} from '../interfaces/game.interface';
import autopopulate from 'mongoose-autopopulate';

  const schema = new Schema<IGame>({
    teamA: {type: Schema.Types.ObjectId, ref: "Team", autopopulate: true},
    teamB: {type: Schema.Types.ObjectId, ref: "Team", autopopulate: true},
    category: {type: Schema.Types.ObjectId, ref: "Category", autopopulate: true},
    hall: {type: Schema.Types.ObjectId , ref: "Hall", autopopulate: true},
    timeslot: {type: Schema.Types.ObjectId, ref: "Timeslot", autopopulate: true},
    type: {type: String},
    pointsTeamA: { type: Number },
    pointsTeamB: { type: Number },
  });
  schema.plugin(autopopulate);
export const Game = model<IGame>('Game', schema);