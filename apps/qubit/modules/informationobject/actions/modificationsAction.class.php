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

class InformationObjectModificationsAction extends sfAction
{
  public function execute($request)
  {
    $this->resource = $this->getRoute()->resource;

    // Check that this isn't the root
    if (!isset($this->resource->parent))
    {
      $this->forward404();
    }

    // Check user authorization
    if (!QubitAcl::check($this->resource, 'read'))
    {
      QubitAcl::forwardToSecureAction();
    }

    $limit = sfConfig::get('app_hits_per_page');

    $page = 1;
    if (isset($request->page) && ctype_digit($request->page))
    {
      $page = $request->page;
    }

    // Term ID to term map
    $this->userActions = array(
      QubitTerm::USER_ACTION_CREATION_ID => QubitTerm::getById(QubitTerm::USER_ACTION_CREATION_ID),
      QubitTerm::USER_ACTION_MODIFICATION_ID => QubitTerm::getById(QubitTerm::USER_ACTION_MODIFICATION_ID)
    );

    // Criteria to fetch user actions
    $criteria = new Criteria;
    $criteria->add(QubitAuditLog::OBJECT_ID, $this->resource->id);
    $criteria->addDescendingOrderByColumn('created_at');

    // Page results
    $this->pager = new QubitPager('QubitAuditLog');
    $this->pager->setCriteria($criteria);
    $this->pager->setPage($page);
    $this->pager->setMaxPerPage($limit);

    $this->modifications = $this->pager->getResults();
  }
}
