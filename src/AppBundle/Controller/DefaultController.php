<?php
/**
 * Author: Vikas Khatana
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $rootDir = $this->get('kernel')->getRootDir();
        $jsonPath = $rootDir.'/../src/AppBundle/JsonData/teamData.json';

        $teamsList = json_decode(file_get_contents($jsonPath),true);
        $teamsByGroups = $this->getTeamsByGroups($teamsList);
        if(empty($teamsByGroups)){
            return $this->redirectToRoute('homepage');
        }
        // replace this example code with whatever you need
        $data = array(
            'teamsList'=>$teamsList,
            'teamsByGroups'=>$teamsByGroups
        );
        return $this->render('default/index.html.twig', $data);
    }

    /**
     * @param $teamsList
     * @return array
     */
    private function getTeamsByGroups($teamsList)
    {
        $groupedTeams = array(
            'Group_A' => array(),
            'Group_B' => array(),
            'Group_C' => array(),
            'Group_D' => array(),
            'Group_E' => array(),
            'Group_F' => array(),
            'Group_G' => array(),
            'Group_H' => array()
        );
        if(count($teamsList) != 32 || count($groupedTeams) !=8){
            echo "Mismatched gropus and team count";die;
        }
        $domesticChampions = array();
        foreach ($teamsList as $key=>$team) {
            if($team['domestic_champ']){
                array_push($domesticChampions,$team);
                unset($teamsList[$key]);
            }
        }

        $count = 1;
        while(!empty($teamsList)){
            //iterate through each group and add a team to it
            foreach ($groupedTeams as &$group){
                if(!empty($teamsList)){
                    $addAndGoToNext = true;
                    //generate random team from teams list
                    $randomKey = array_rand($teamsList);
                    $randomTeam = $teamsList[$randomKey];
                    if(!empty($group)){
                        foreach ($group as $includedTeams){
                            if(count($group) == 4 ){
                                $addAndGoToNext = false;
                            }
                            if($randomTeam['country']  == $includedTeams['country']){
                                $addAndGoToNext = false;
                            }
                        }
                    }else{
                        //first team in any group is champion team
                        $randomChampionTeamKey = array_rand($domesticChampions);
                        $randomChampTeam = $domesticChampions[$randomChampionTeamKey];
                        array_push($group,$randomChampTeam);
                        unset($domesticChampions[$randomChampionTeamKey]);
                    }
                    //if all if fine add team to group
                    if($addAndGoToNext){
                        array_push($group,$randomTeam);
                        unset($teamsList[$randomKey]);
                    }
                }
            }
            $count++;
            //no random group could be generated in this session..lets reload page
            if($count>8){
                return array();
            }
        }
        return $groupedTeams;
    }
}