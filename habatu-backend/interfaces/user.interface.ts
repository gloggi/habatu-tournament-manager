import { Role } from "./role";
export interface IUser {
    nickname: string;
    password: string;
    roles: Role[];
    team: string;
    token?: string;
  }