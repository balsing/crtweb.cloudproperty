<?php

namespace Crtweb\CloudProperty\Connectors;

use Crtweb\CloudProperty\File\File;

interface ConnectorInterface
{
    /**
     * ����� ������ ���������� ������ �� ���� �� id, ���� ����� �������������� �� �������� VALUE ��������
     *
     * @param $file_id
     * @return string - link to file
     */
    public function get($file_id);
    
    /**
     * ����� ������ ��������� ���� � ������� ��� ������������� ��� ������ � ���� ������ ��� �������� VALUE ��������
     *
     * @param File $file
     * @return string - file id or name
     */
    public function save(File $file);
    
    /**
     * ����� ��������� ������������� � ���������� ����� �� ��� ��������������
     *
     * @param $file_id
     * @return boolean - file check result
     */
    public function check($file_id);
    
    /**
     * ����� ������� ���� �� ��� ��������������
     *
     * @param $file_id
     * @return true
     */
    public function delete($file_id);
    
    /**
     * ����� ���������� ��� ����������� ���������� � ��������� �������.
     *
     * @return mixed
     */
    public function auth();
}
