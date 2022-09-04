import { ITeam } from "../interfaces/team.interface"
import { IGame } from "../interfaces/game.interface"
import { ICategory } from "../interfaces/category.interface"
import { IHall } from "../interfaces/hall.interface"
import { Document, HydratedDocument, Types } from 'mongoose';
import sortBy from "lodash/sortBy";

export const gamesGenerator = (teams: HydratedDocument<ITeam>[], categories: HydratedDocument<ICategory>[], halls: HydratedDocument<IHall>[]): IGame[] => {
    const games: IGame[] = []
    const teamsByCategory: Record<string, HydratedDocument<ITeam>[]> = {}
    for (let category of categories) {
        teamsByCategory[category._id.toString()] = teams.filter((team) => team.category == category._id.toString())
    }
    console.log("Category: ", teams[0].category)
    const gamesByCategory: Record<string, IGame[]> = {}
    for (let category in teamsByCategory) {
        const teams = teamsByCategory[category]
        const teamsById: string[] = teams.map(team=>team._id.toString())
        const DUMMY: string = ""
        if(teamsById.length%2==1){
            teamsById.push(DUMMY)
        }
        for(let i=0;i<teamsById.length-1;i++){
            const o = teams.length - 1 - i;
            for(let j=0; j<teamsById.length/2;j++){
                if(teamsById[i]!==DUMMY||teamsById[o]!==DUMMY){
                    const game: IGame ={
                        teamA: teamsById[i],
                        teamB: teamsById[o],
                        category: category,
                        hall: "",
                        pointsTeamA: 0,
                        pointsTeamB: 0
                    }
                    games.push(game)
                    console.log()

                }

            }
        }

    }
    console.log("GAMES from rrR", games)
    return games
} 

const roundRobin = (teams:string[])=>{
    const DUMMY = ""
    const rounds = []
    if(teams.length%2==1){
        teams.push(DUMMY)
    }
    for(let i = 0;i<teams.length-1;i++){
        const o = teams.length - 1 - i;
        for(let j=0;j<teams.length/2;j++){
            if(teams[i]!==""||teams[o]!==""){

            }

        }
    }
}