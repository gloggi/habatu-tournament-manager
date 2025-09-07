export type IHall = {
  id?: number;
  name: string;
};

export interface IForm {
  label: string;
  key: string;
  type: string;
  required: boolean;
  optionsEntity?: string;
}

export type Team = {
  id: number;
  name: string;
  sectionId?: number;
  section?: Section;
  categoryId: number;
  createdAt: string;
  updatedAt: string;
};

export type Hall = {
  id: number;
  name: string;
  createdAt: string;
  updatedAt: string;
};

export type Section = {
  id: number;
  name: string;
  createdAt: string;
  updatedAt: string;
};

export type Timeslot = {
  id: number;
  startTime: string;
  endTime: string;
  createdAt: string;
  updatedAt: string;
};

export type Category = {
  id: number;
  name: string;
  color: string | null;
  teams?: Team[];
  createdAt: string;
  updatedAt: string;
};

export type Game = {
  id: number;
  teamAId: number;
  teamBId: number;
  categoryId: number;
  hallId: number;
  timeslotId: number;
  type: string | null;
  pointsTeamA: number;
  pointsTeamB: number;
  createdAt: string;
  updatedAt: string;
  teamA: Team;
  teamB: Team;
  hall: Hall;
  timeslot: Timeslot;
  category: Category;
  referees: User[];
  played: boolean;
  classes: string;
  finalTypeLabel?: string;
  hasReferee: boolean;
};

export type SlotInfo = {
  hallId: number;
  timeslotId: number;
  hallName: string;
  hasGames: boolean;
};

export type ScheduleEntry = {
  slotInfo: SlotInfo;
  games: Game[];
};

export type Schedule = Record<string, Record<string, ScheduleEntry>>;

export type User = {
  id: number;
  nickname: string;
  created_at?: string;
  updated_at?: string;
  teamId?: number;
  sectionId?: number;
  role?: string;
  section?: Section;
  team?: Team;
};

export type LoginAnswer = {
  token: string;
  user: User;
};

export type RankedTeam = {
  rank: number;
  team: Team;
  matchesPlayed: number;
  wins: number;
  draws: number;
  losses: number;
  points: number;
  goalsScored: number;
  goalsConceded: number;
  goalsDifference: number;
};

export type GroupRanking = {
  groupName: string;
  ranking: RankedTeam[];
};

export type CategoryRanking = {
  categoryName: string;
  groups: GroupRanking[];
};

export type CategoryRankings = CategoryRanking[];

export interface Options {
  id: number;
  tournamentName?: string;
  startTime?: string;
  gameDuration?: number;
  breakDuration?: number;
  additionalSlots?: number;
  startedTournament: boolean;
  endedRoundGames: boolean;
  createdAt?: string;
  updatedAt?: string;
}

export enum TableType {
  Normal = "NORMAL",
  Referee = "REFEREE",
  Team = "TEAM",
}

export type Message = {
  userId: number;
  title: string;
  body: string;
};
