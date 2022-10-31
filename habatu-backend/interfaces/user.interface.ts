import { Role } from "./role";
export interface IUser {
    nickname: string;
    password: string;
    role: Role;
    team: string;
    token?: string;
  }