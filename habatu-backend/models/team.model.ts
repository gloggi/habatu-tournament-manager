import { Schema, model, connect } from 'mongoose';
import {ITeam} from '../interfaces/team.interface';
import autopopulate from 'mongoose-autopopulate';

  const schema = new Schema<ITeam>({
    name: { type: String, required: true },
    section: {type: Schema.Types.ObjectId, ref: "Section", autopopulate: true},
    category: {type: Schema.Types.ObjectId, ref: "Category", autopopulate: true},
  });
  schema.plugin(autopopulate);
export const Team = model<ITeam>('Team', schema);