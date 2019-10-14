<?php

namespace App\Builder;

use Symfony\Component\HttpFoundation\Request;
use \Datetime;
use App\Entity\Show;

class ShowBuilder
{
    public function __construct()
    {
    }

    /**
     * @param Show[] $show
     * @return array
     */
    public function buildList($shows)
    {
        $dataResponse = array();
        foreach ($shows as $show) {
            $dataResponse[] = $this->build($show);
        }

        return $dataResponse;
    }

    /**
     * @param Show $show
     * @return array
     */
    public function build( $show)
    {
        $totalSeasons = 0;
        if(isset($show->totalSeasons)){
            $totalSeasons = $show->totalSeasons;
        }
        return array(
            'title' => $show->Title,
            'year' => $show->Year,
            'poster' => $show->Poster,
            'type' => $show->Type,
            'totalSeasons' => $totalSeasons,
        );
    }

    /**
     * @return array
     */
    public function buildFreshShow(Request $request)
    {
        $show = new Show();
        return $this->buildShow($show, $request);
    }

    /**
     * @param Show $show
     * @return array
     */
    public function buildShow(Show $show, Request $request)
    {
        $show->setNumber($request->get('number') ?: $show->getNumber());
        $show->setBuildingNumber($request->get('building_number') ?: $show->getBuildingNumber());
        $show->setFloorNumber($request->get('floor_number') ?: $show->getFloorNumber());

        $residenceId = $request->get('residence_id');
        if($residenceId){
            $residence = $this->residenceFinder->findResidence($residenceId);
            $show->setResidence($residence);
        }

        if(!$show->getCreationDate()){
            $show->setCreationDate(new \DateTime());
        }

        return $show;
    }
}
