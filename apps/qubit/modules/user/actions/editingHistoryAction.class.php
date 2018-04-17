<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

class UserEditingHistoryAction extends sfAction
{
  public function execute($request)
  {
    $this->resource = $this->getRoute()->resource;

    $limit = sfConfig::get('app_hits_per_page');

    $page = 1;
    if (isset($request->page) && ctype_digit($request->page))
    {
      $page = $request->page;
    }

    // Term ID to term map
    $userActions = array(
      QubitTerm::USER_ACTION_CREATION_ID => QubitTerm::getById(QubitTerm::USER_ACTION_CREATION_ID),
      QubitTerm::USER_ACTION_MODIFICATION_ID => QubitTerm::getById(QubitTerm::USER_ACTION_MODIFICATION_ID)
    );

    // Criteria to fetch user actions
    $criteria = new Criteria;
    $criteria->add(QubitAuditLog::USER_ID, $this->resource->id);
    $criteria->addDescendingOrderByColumn('created_at');

    // Page results
    $pager = new QubitPager('QubitAuditLog');
    $pager->setCriteria($criteria);
    $pager->setPage($page);
    $pager->setMaxPerPage($limit);

    // Summarize results
    $results = array();

    foreach ($pager->getResults() as $modification)
    {
      $result = array(
        'createdAt' => $modification->createdAt,
        'objectId' => $modification->objectId,
        'actionType' => $userActions[$modification->actionTypeId]->name
      );

      array_push($results, $result);    
    }

    // Return results and paging data
    $data = array(
      'results' => $results,
      'items' => $pager->getNbResults(),
      'pages' => $pager->getLastPage()
    );

    return $this->renderText(json_encode($data));
  }
}
