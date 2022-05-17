import { ITeam } from "../interfaces/team.interface"
import { IGame } from "../interfaces/game.interface"
import { ICategory } from "../interfaces/category.interface"
import { IHall } from "../interfaces/hall.interface"
import { HydratedDocument } from 'mongoose';

export const roundRobin = (teams: HydratedDocument<ITeam>[], categories: HydratedDocument<ICategory>[], halls: HydratedDocument<IHall>[]): IGame[] => {
    const games: IGame[] = []

    for (let category of categories) {
        for (let teamA of teams) {
            for (let teamB of teams) {
                if(teamB._id!=teamA._id&&teamA.category==teamB.category&&teamA.category==category._id.toString()){
                let game: IGame = { teamA: teamA._id.toString(), 
                    teamB: teamB._id.toString(), 
                    category: category._id.toString(),
                    hall: "",
                    pointsTeamA:0,
                    pointsTeamB: 0
                 }
                 games.push(game)
                }

            }
        }

    }
    return games
} 