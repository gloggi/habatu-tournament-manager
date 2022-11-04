import { Role } from "./role";

export interface IJWTPayload {
  user_id: string;
  nickname: string;
  roles: Role[];
  iat: number;
  exp: number;
}
