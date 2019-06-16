<?php
/**
 * Author: Vikas Khatana
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * Description : Home page for this project
     * @param Request $request
     * @return Response
     * @Route("/vikas", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $rootDir = $this->get('kernel')->getRootDir();
        $jsonPath = $rootDir.'/../src/AppBundle/JsonData/teamData.json';

        //read all data from json file and store in array.
        $teamsList = json_decode(file_get_contents($jsonPath),true);
        $teamsByGroups = $this->getTeamsByGroups($teamsList);
        if(empty($teamsByGroups)){
            return $this->redirectToRoute('homepage');
        }
        // data for template
        $data = array(
            'teamsList'=>$teamsList,
            'teamsByGroups'=>$teamsByGroups
        );
        return $this->render('default/index.html.twig', $data);
    }

    /**
     * Description : Get randomly generated groups and teams
     * @param $teamsList
     * @return array
     */
    private function getTeamsByGroups($teamsList)
    {
        $groupedTeams = array(
            'Group A' => array(),
            'Group B' => array(),
            'Group C' => array(),
            'Group D' => array(),
            'Group E' => array(),
            'Group F' => array(),
            'Group G' => array(),
            'Group H' => array()
        );

        $moreThan8SameCountries = false;
        $countriesArray = array();
        foreach ($teamsList as $team){
            $country = $team['country'];
            if(array_key_exists($country,$countriesArray)){
                $countriesArray[$country] +=1;
            }else{
                $countriesArray[$country] =1;
            }
        }
        foreach ($countriesArray as $value){
            if($value > 8){
                $moreThan8SameCountries = true;
            }
        }
        //some sanitary checks
        if(count($teamsList) != 32 || count($groupedTeams) !=8 || $moreThan8SameCountries == true){
            echo "Mismatched gropus and team count or more than 8 similar countries";die;
        }
        $domesticChampions = array();
        foreach ($teamsList as $key=>$team) {
            if($team['domestic_champ']){
                array_push($domesticChampions,$team);
                unset($teamsList[$key]);
            }
        }
        //some sanitary checks ...if domestic champs are not 8 , grouping is not possible
        if(count($domesticChampions) !=8){
            echo "domestic champs are not 8 , grouping is not possible";die;
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
                        //we dont want to insert any team further , since all checks are above
                        $addAndGoToNext = false;
                    }
                    //if all if fine add team to group
                    if($addAndGoToNext){
                        array_push($group,$randomTeam);
                        unset($teamsList[$randomKey]);
                    }
                }
            }
            $count++;
            //no random group could be generated retry.
            if($count>8){
                return array();
            }
        }
        return $groupedTeams;
    }
}
