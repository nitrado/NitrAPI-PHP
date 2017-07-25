<?php
namespace Nitrapi\GameInfo;

use Nitrapi\Common\NitrapiObject;
use Nitrapi\Nitrapi;

class GameInfo extends NitrapiObject{

    private $data;

    public function __construct(Nitrapi &$api, &$data) {
        parent::__construct($api);
        $this->data = $data;
    }

    public function getId() {
        return $this->data['id'];
    }

    public function getName() {
        return $this->data['name'];
    }

    public function getMinimumSlots() {
        return $this->data['minimum_slots'];
    }

    public function getMaximumRecommendedSlots() {
        return $this->data['maximum_recommended_slots'];
    }

    public function getLocationIds() {
        return $this->data['locations'];
    }

    public function getSlotMultiplier() {
        return $this->data['slot_multiplier'];
    }

    public function getFolderShort() {
        return $this->data['folder_short'];
    }

    public function getPortlistShort() {
        return $this->data['portlist_short'];
    }
}