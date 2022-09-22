import { Schema, model, connect } from 'mongoose';
import {ITimeslot} from '../interfaces/timeslot.interface';

  const schema = new Schema<ITimeslot>({
    startTime: {type: Date},
    endTime: {type: Date},
    games: [{type: Schema.Types.ObjectId, ref: "Game", autopopulate: true}],
  });
export const Timeslot = model<ITimeslot>('Timeslot', schema);