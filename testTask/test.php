<?php
/*
    Необходимо доработать класс рассылки Newsletter, что бы он отправлял письма
    и пуш нотификации для юзеров из UserRepository.

    За отправку имейла мы считаем вывод в консоль строки: "Email {email} has been sent to user {name}"
    За отправку пуш нотификации: "Push notification has been sent to user {name} with device_id {device_id}"

    Так же необходимо реализовать функциональность для валидации имейлов/пушей:
    1) Нельзя отправлять письма юзерам с невалидными имейлами
    2) Нельзя отправлять пуши юзерам с невалидными device_id. Правила валидации можете придумать сами.
    3) Ничего не отправляем юзерам у которых нет имен
    4) На одно и то же мыло/device_id - можно отправить письмо/пуш только один раз

    Для обеспечения возможности масштабирования системы (добавление новых типов отправок и новых валидаторов),
    можно добавлять и использовать новые классы и другие языковые конструкции php в любом количестве
*/
class Validate
{
    public static function validateEmail($email)
    {

        if (isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else return false;
    }

    /* Минимальное допустимое количество символов device_id = 6 символов
     */
    public static function validateDevice($device_id)
    {
        if (isset($device_id) && !empty($device_id) )
        {
            return true;
        }
        else return false;
    }

    public static function validateName($name)
    {
        if (!empty($name)) {
            return true;
        }
        else false;
    }

}
class Newsletter
{
    private $email = [];
    private $device_id = [];
    private $email_user_name = [];
    private $device_user_name = [];

    public function sendEmail()
    {
        $userRepository = new UserRepository();
        $users = $userRepository->getUsers();
        foreach ($users as $user)
        {
            $email = isset($user['email']) ? $user['email'] : null;
            $name = isset($user['name']) ? $user['name'] : null;

            $validEmail = Validate::validateEmail($email);
            $validName = Validate::validateName($name);
            if ($validEmail && $validName)
            {
                if(!in_array($email, $this->email))
                {
                    array_push($this->email ,  $email);
                    array_push( $this->email_user_name , $name);
                }
            }
        }

    }
    public function sendDeviceId()
    {
        $userRepository = new UserRepository();
        $users = $userRepository->getUsers();
        foreach ($users as $user)
        {

            $name = isset($user['name']) ? $user['name'] : null;
            $device_id = isset($user['device_id']) ? $user['device_id'] : null;

            $validName = Validate::validateName($name);
            $validDeviceId = Validate::validateDevice($device_id);
            if ($validName && $validDeviceId)
            {
                if (!in_array($device_id, $this->device_id))
                {
                    array_push($this->device_id, $device_id);
                    array_push($this->device_user_name, $name);
                }

            }
        }
    }

    public function send(): void
    {
        for($i = 0; $i < count($this->email); $i++)
        {
            echo "Email '" . $this->email[$i] . "' has been sent to user '" . $this->email_user_name[$i] . "'". PHP_EOL;
        }
        for ($i = 0; $i < count($this->device_id); $i++)
        {
            echo "Push notification has been sent to user '" . $this->device_user_name[$i] . "' with device_id '" . $this->device_id[$i] . "'" . PHP_EOL;
        }

    }

}



class UserRepository
{
    public function getUsers(): array
    {
        return [
            [
                'name' => 'Ivan',
                'email' => 'ivan@test.com',
                'device_id' => 'Ks[dqweer4'
            ],
            [
                'name' => 'Peter',
                'email' => 'peter@test.com'
            ],
            [
                'name' => 'Mark',
                'device_id' => 'Ks[dqweer4'
            ],
            [
                'name' => 'Nina',
                'email' => '...'
            ],
            [
                'name' => 'Luke',
                'device_id' => 'vfehlfg43g'
            ],
            [
                'name' => 'Zerg',
                'device_id' => ''
            ],
            [
                'email' => '...',
                'device_id' => ''
            ]
        ];
    }
}

/**
Тут релизовать получение объекта(ов) рассылки Newsletter и вызов(ы) метода send()
$newsletter = //... TODO
$newsletter->send();
...
 */

$newsletter = new Newsletter();
$newsletter->sendEmail();
$newsletter->sendDeviceId();
$newsletter->send();