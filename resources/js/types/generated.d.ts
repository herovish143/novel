declare namespace Domain {
    namespace Novel {
        namespace Data {
            export type NovelData = {
                id: number;
                title: string;
                slug: string;
                originalLanguage: string;
                outputLanguage: string;
                sourceUrl: string | null;
                description: string | null;
                visualStyle: string;
                narrationStyle: string;
                maxCostPerEpisode: number;
                status: string;
                chaptersCount: number | null;
                charactersCount: number | null;
                locationsCount: number | null;
            };
        }
    }
}
