import { Schema, model, connect } from 'mongoose';
import {ISection} from '../interfaces/section.interface';

  const schema = new Schema<ISection>({
    name: { type: String, required: true }
  });
export const Section = model<ISection>('Section', schema);