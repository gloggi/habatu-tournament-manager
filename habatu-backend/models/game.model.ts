import { Schema, model, connect } from 'mongoose';
import {IGame} from '../interfaces/game.interface';
import autopopulate from 'mongoose-autopopulate';

  const schema = new Schema<IGame>({
    teamA: {type: Schema.Types.ObjectId, ref: "Team", autopopulate: true},
    teamB: {type: Schema.Types.ObjectId, ref: "Team", autopopulate: true},
    category: {type: Schema.Types.ObjectId, ref: "Category", autopopulate: true},
    hall: {type: Schema.Types.ObjectId, ref: "Hall", autopopulate: true},
    startTime: { type: Date },
    endTime: { type: Date },
    pointsTeamA: { type: Number },
    pointsTeamB: { type: Number },
  });
  schema.plugin(autopopulate);
export const Game = model<IGame>('Game', schema);