import { Role } from "./role";
export interface IUser {
    nickname: string;
    password: string;
    roles: Role[];
    group: string;
    token?: string;
  }