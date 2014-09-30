<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/TicketsDBProcessor.php');

class TicketsCacheProcessor extends BaseCacheProcessor implements IProcessor
{
    const TICKETS_CACHE_KEY = "tickets::player::%s";

    public function init()
    {
        $this->setBackendProcessor(new TicketsDBProcessor());
    }

    public function create(Entity $ticket)
    {
        $ticket = $this->getBackendProcessor()->create($ticket);
        $this->recachePlayerTickets($ticket->getPlayerId());
        
        return $ticket;
    }

    protected function recachePlayerTickets($playerId)
    {
        $player = new Player();
        $player->setId($playerId);
        if (!Cache::init()->set($this->getCacheKey($playerId), $this->getBackendProcessor()->getPlayerTickets($player))) {
            throw new ModelException("Unable to cache storage data", 500);
        }
    }

    protected function getCacheKey($playerId)
    {
        return sprintf(self::TICKETS_CACHE_KEY, $playerId);
    }

    public function update(Entity $ticket) {
        return $ticket;
    }

    public function fetch(Entity $ticket)
    {
        return $ticket;
    }

    public function delete(Entity $tiket) 
    {
        return true;
    }

    public function getPlayerTickets($player)
    {
        if (!($tickets = Cache::init()->get($this->getCacheKey($player->getId())))) {
            $tickets = $this->getBackendProcessor()->getPlayerTickets($player);

            $this->recachePlayerTickets($player->getId());
        }

        return $tickets;
    }
}