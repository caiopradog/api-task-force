<?php
namespace App\Constants;

class TasksStatusConstant
{

    use \App\ConstantTrait;

    CONST BACKLOG = 'Backlog';

    CONST PENDING = 'Pendente';

    CONST DEVELOPING = 'Em Andamento';

    CONST TESTING = 'Qualidade';

    CONST DONE = 'Finalizado';

}
