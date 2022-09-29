import { Schema, model, connect } from 'mongoose';
import {IUser} from '../interfaces/user.interface';
import autopopulate from 'mongoose-autopopulate';

  const schema = new Schema<IUser>({
    nickname: {type: String, unique: true, required: true},
    password: {type: String},
    token: {type: String},
  });
  schema.plugin(autopopulate);
export const User = model<IUser>('User', schema);