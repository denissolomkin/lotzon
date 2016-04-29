<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

abstract class DBProcessor implements IProcessor
{
    public function beginTransaction()
    {
        try {
            DB::Connect()->beginTransaction();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        return true;
    }

    public function commit()
    {
        try {
            DB::Connect()->commit();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        return true;
    }

    public function rollBack()
    {
        try {
            DB::Connect()->rollBack();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        return true;
    }
}

