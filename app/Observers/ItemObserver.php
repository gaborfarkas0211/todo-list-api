<?php

namespace App\Observers;

use App\Models\Item;
use Stevebauman\Purify\Facades\Purify;

class ItemObserver
{
    /**
     * Handle the Item "save" event.
     *
     * @param \App\Item $item
     * @return void
     */
    public function saving(Item $item)
    {
        $this->purify($item);
    }

    /**
     * Handle the Item "update" event.
     *
     * @param \App\Models\Item $item
     * @return void
     */
    public function updating(Item $item)
    {
        $this->purify($item);
    }

    private function purify(Item $item): void
    {
        $cleanableAttributes = ['name'];
        if (null !== $item->description) {
            $cleanableAttributes[] = 'description';
        }

        foreach ($cleanableAttributes as $attribute) {
            $item->$attribute = Purify::clean($item->$attribute);
        }
    }
}
