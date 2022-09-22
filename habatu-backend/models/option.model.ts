import { Schema, model, connect } from 'mongoose';
import {IOption} from '../interfaces/option.interface';

  const schema = new Schema<IOption>({
    tournamentName: { type: String},
    startTime: { type: Date},
    gameDuration: { type: Date},
    breakDuration: { type: Date}
  });
export const Option = model<IOption>('Option', schema);