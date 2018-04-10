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

class InformationObjectCalculateDatesAction extends sfAction
{
  public function execute($request)
  {
    $this->resource = $this->getRoute()->resource;

    $params = array(
      'objectId' => $this->resource->id,
      'eventId' => $request->getParameter('eventId')
    );

    // Catch no Gearman worker available exception
    // and others to show alert with exception message
    try
    {
      QubitJob::runJob('arCalculateAccumulatedDatesJob', $params);
    }
    catch (Exception $e)
    {
      $this->response->setStatusCode(500);

      return $this->renderText(json_encode(array('error' => $this->context->i18n->__('Calculation failed: ') . $e->getMessage())));
    }

    $message = $this->context->i18n->__('Accumulated date calculation started.');
    $this->context->user->setFlash('info', $message);

    $this->redirect(array($this->resource, 'module' => 'informationobject'));
  }
}
