import { Schema, model, connect } from 'mongoose';
import {IGame} from '../interfaces/game.interface';

  const schema = new Schema<IGame>({
    teamA: {type: Schema.Types.ObjectId, ref: "Team", autopopulate: true},
    teamB: {type: Schema.Types.ObjectId, ref: "Team", autopopulate: true},
    category: {type: Schema.Types.ObjectId, ref: "Category", autopopulate: true},
    hall: {type: Schema.Types.ObjectId, ref: "Hall", autopopulate: true},
    pointsTeamA: { type: Number },
    pointsTeamB: { type: Number },
  });
export const Game = model<IGame>('Game', schema);