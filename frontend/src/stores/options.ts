import { defineStore } from 'pinia';
import { useApi } from '@/api';
import { Options } from '@/types';

export const useOptionsStore = defineStore('options', {
    state: () => ({
        options: null as Options | null,
    }),
    
    getters: {
        getTournamentName: (state) => state.options?.tournamentName ?? '',
        getStartTime: (state) => state.options?.startTime ?? '',
        getGameDuration: (state) => state.options?.gameDuration ?? 0,
        getBreakDuration: (state) => state.options?.breakDuration ?? 0,
        getAdditionalSlots: (state) => state.options?.additionalSlots ?? 0,
        isTournamentStarted: (state) => state.options?.startedTournament ?? false,
        hasEndedRoundGames: (state) => state.options?.endedRoundGames ?? false,
    },
    
    actions: {
        async fetchOptions() {
            if (this.options) return;
    
            const { fetchData, data } = useApi<Options>('options');
            await fetchData(undefined, true);
            this.options = data.value;
        },
        
        setOptions(optionsData: Options) {
            this.options = optionsData;
        },
        
        clearOptions() {
            this.options = null;
        },
    },
});
