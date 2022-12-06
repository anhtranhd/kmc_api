<?php namespace App\Http\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;
use App\Services\StorageService;

class UserTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(User $item)
  {
      return [
            "id" => (int)$item->id,
            "userName" => $item->user_name,
            "fullName" => $item->full_name,
            "email" => $item->email,
            "avatar" => $this->getFile($item),
            "phone" => $item->phone,
            "gender" => $item->gender,
            "birthday" => $item->birthday,
            "province" => $item->province,
            "city" => $item->city,
            "address" => $item->address,
            "storeId" => $item->store_id,
            "storeName" => $item->store_name,
            "permissions" => $item->permissions,
            "lastLogin" => $item->last_login,
            "createdAt" => (string)$item->created_at,
            "updatedAt" => (string)$item->updated_at
      ];
  }

  private function getFile($user) {
    $mediaFile = $user->medialImageable()->first();
    if ($mediaFile) {
        $storageService = new StorageService();
        return $storageService->url($mediaFile->file->path);
    }
    return null;
  }
}
