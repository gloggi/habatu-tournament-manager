import { Types } from 'mongoose';
import { ISection } from './section.interface';
export interface IRankedTeam {
    _id: Types.ObjectId | string;
    name: string;
    section?: ISection;
    category?: string;
    tournamentPoints?: number;
    pointsPro?: number;
    pointsCon?: number;
  }