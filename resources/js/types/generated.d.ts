declare namespace Domain {
    namespace DocumentImport {
        namespace Enums {
            export type ChapterCandidateStatus =
                | 'DETECTED'
                | 'REVIEW_REQUIRED'
                | 'APPROVED'
                | 'SKIPPED'
                | 'IMPORTED'
                | 'DUPLICATE'
                | 'FAILED';
            export type DocumentImportStatus =
                | 'UPLOADED'
                | 'EXTRACTING'
                | 'DETECTING'
                | 'REVIEW_REQUIRED'
                | 'IMPORTING'
                | 'COMPLETED'
                | 'FAILED';
            export type ExtractionMethod =
                'NATIVE' | 'OCR' | 'NATIVE_WITH_OCR_FALLBACK';
        }
    }
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
