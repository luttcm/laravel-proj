<?php

namespace App\Services;

use App\Repositories\NewsRepository;
use App\Repositories\PictureRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NewsService
{
    /** @var NewsRepository */
    protected $newsRepository;
    /** @var PictureRepository */
    protected $pictureRepository;

    public function __construct(NewsRepository $newsRepository, PictureRepository $pictureRepository)
    {
        $this->newsRepository = $newsRepository;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\News>
     */
    public function getAllNews()
    {
        $news = $this->newsRepository->getAllWithComments();

        foreach ($news as $n) {
            $pictures = $this->pictureRepository->getByEntity('news', $n->id, 9);
            $n->pictures = $pictures->map(fn($p) => asset($p->path));
            $n->firstPicture = $n->pictures->first();
            $n->comments_count = $n->comments->count();
        }

        return $news;
    }

    /**
     * @param int $id
     * @return array{newsItem: \App\Models\News|null, pictures: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Picture>}
     */
    public function getNewsDetails(int $id): array
    {
        $newsItem = $this->newsRepository->findById($id);
        $pictures = $this->pictureRepository->getByEntity('news', $id, 9);
        
        return [
            'newsItem' => $newsItem,
            'pictures' => $pictures
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, UploadedFile>|null $images
     * @param int|null $authorId
     * @return array<string, mixed>
     */
    public function createNews(array $data, array $images = null, ?int $authorId): array
    {
        $news = $this->newsRepository->create([
            'title' => $data['title'],
            'content' => $data['content'],
            'author_id' => $authorId,
            'reactions' => 0,
        ]);

        $message = 'Новость добавлена';
        if ($images) {
            $result = $this->handleImages($news->id, $images, 9);
            if ($result['skipped'] > 0) {
                $message .= " ({$result['skipped']} картинок пропущено - максимум 9)";
            }
        }

        return ['news' => $news, 'message' => $message];
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @param array<int, UploadedFile>|null $images
     * @return string
     */
    public function updateNews(int $id, array $data, array $images = null): string
    {
        $this->newsRepository->update($id, [
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        if ($images) {
            $existingCount = $this->pictureRepository->countByEntity('news', $id);
            $canAdd = max(0, 9 - $existingCount);
            if ($canAdd > 0) {
                $this->handleImages($id, $images, $canAdd);
            }
        }

        return 'Новость обновлена';
    }

    public function deleteNews(int $id): void
    {
        $this->deleteEntityPictures('news', $id);
        $this->newsRepository->delete($id);
    }

    /**
     * @param int $newsId
     * @param array<int, int> $likedSessions
     * @return array<string, mixed>
     */
    public function toggleLike(int $newsId, array $likedSessions): array
    {
        if (in_array($newsId, $likedSessions)) {
            $this->newsRepository->decrementReactions($newsId);
            $likedSessions = array_values(array_diff($likedSessions, [$newsId]));
            $isLiked = false;
        } else {
            $this->newsRepository->incrementReactions($newsId);
            $likedSessions[] = $newsId;
            $isLiked = true;
        }
        
        $news = $this->newsRepository->findById($newsId);

        if (!$news) {
            return [
                'liked_news' => $likedSessions,
                'reactions' => 0,
                'liked' => $isLiked,
            ];
        }

        return [
            'liked_news' => $likedSessions,
            'reactions' => $news->reactions,
            'liked' => $isLiked,
        ];
    }
    
    public function deletePicture(int $pictureId): bool
    {
        $picture = $this->pictureRepository->findById($pictureId);
        
        if ($picture && $picture->path && Storage::disk('public')->exists(str_replace('storage/', '', (string)$picture->path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', (string)$picture->path));
        }

        return $this->pictureRepository->delete($pictureId);
    }

    protected function deleteEntityPictures(string $entityType, int $entityId): void
    {
        $pictures = $this->pictureRepository->getByEntity($entityType, $entityId);
        foreach ($pictures as $picture) {
            if ($picture->path && Storage::disk('public')->exists(str_replace('storage/', '', (string)$picture->path))) {
                Storage::disk('public')->delete(str_replace('storage/', '', (string)$picture->path));
            }
        }
        $this->pictureRepository->deleteByEntity($entityType, $entityId);
    }

    /**
     * @param int $newsId
     * @param array<int, UploadedFile> $images
     * @param int $limit
     * @return array<string, int>
     */
    protected function handleImages(int $newsId, array $images, int $limit): array
    {
        $imageCount = 0;
        $skipped = 0;
        foreach ($images as $file) {
            if ($imageCount >= $limit) {
                $skipped++;
                continue;
            }
            $path = $file->store('news', 'public');
            $this->pictureRepository->create([
                'path' => 'storage/' . $path,
                'entity_type' => 'news',
                'entity_id' => $newsId,
            ]);
            $imageCount++;
        }
        
        return ['added' => $imageCount, 'skipped' => $skipped];
    }
}
