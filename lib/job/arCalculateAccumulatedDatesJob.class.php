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

/**
 * A job to calculate, for a given information object and event type, the
 * earliest start date and latest end date
 *
 * @package    symfony
 * @subpackage jobs
 */

class arCalculateAccumulatedDatesJob extends arBaseJob
{
  /**
   * @see arBaseJob::$requiredParameters
   */
  protected $extraRequiredParameters = array('objectId', 'eventId');

  public function runJob($parameters)
  {
    $this->info($this->i18n->__(
      'Calculating accumulated dates for information object (ID: %1, event ID: %2)',
      array('%1' => $parameters['objectId'], '%2' => $parameters['eventId'])
    ));

    // Load related objects
    $io = QubitInformationObject::getById($parameters['objectId']);
    $event = QubitEvent::getById($parameters['eventId']);

    // Determine earliest start date and lastest end date of descendent events
    // sharing type with provided event
    $sql = "SELECT
      MIN(e.start_date) AS min,
      MAX(e.end_date) AS max
      FROM
        information_object i
        INNER JOIN event e ON i.id=e.object_id
      WHERE
        i.lft > :lft
        AND rgt < :rgt
        AND e.type_id=:eventType";

    $params = array(
      ':lft' => $io->lft,
      ':rgt' => $io->rgt,
      ':eventType' => $event->typeId
    );

    $rangeData = QubitPdo::fetchOne($sql, $params, array('fetchMode' => PDO::FETCH_ASSOC));

    // Update event with start date and end date
    if (!empty($rangeData->min) && !empty($rangeData->max))
    {
      $event->startDate = $rangeData->min;
      $event->endDate = $rangeData->max;
      $event->save();
    }

    // Mark job as completed
    $this->info('Calculation completed.');
    $this->job->setStatusCompleted();
    $this->job->save();

    return true;
  }
}
