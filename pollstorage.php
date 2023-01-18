<?php
class PollStorage extends Storage {
    public function __construct() {
        parent::__construct(new JsonIO('polls.json'));
    }
    public function update(string $id, $record) {
        $this->contents[$id] = $record;
        $this->io->save($this->contents);
    }
}
