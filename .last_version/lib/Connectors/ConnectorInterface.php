<?php

namespace Crtweb\CloudProperty\Connectors;

use Crtweb\CloudProperty\File\File;

interface ConnectorInterface
{
    /**
     * Метод должен возвращать ссылку на файл по id, либо иному идентификатору из значения VALUE свойства
     *
     * @param $file_id
     * @return string - link to file
     */
    public function get($file_id);
    
    /**
     * Метод должен сохранить файл и вернуть его идентификатор для записи в базу данных как значение VALUE свойства
     *
     * @param File $file
     * @return string - file id or name
     */
    public function save(File $file);
    
    /**
     * Метод проверяет существование и доступость файла по его идентификатору
     *
     * @param $file_id
     * @return boolean - file check result
     */
    public function check($file_id);
    
    /**
     * Метод удаляет файл по его идентификатору
     *
     * @param $file_id
     * @return true
     */
    public function delete($file_id);
    
    /**
     * Метод вызывается для авторизации приложения в стороннем сервисе.
     *
     * @return mixed
     */
    public function auth();
}
