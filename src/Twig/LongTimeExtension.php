<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LongTimeExtension extends AbstractExtension
{

    public function getFilters(){

        return array(
            new TwigFilter('long_time', [$this, 'LongTimeFilter'])
        );

    }

    //FORMATEADOR DE FECHA
    public function LongTimeFilter($date) {

        //SI LA FECHA ES NULA, DEVUELVE SIN FECHA
        if ($date == null) {
            return "Sin fecha";
        }

        //CALCULAR LA DIFERENCIA ENTRE LA FECHA ACTUAL Y LA FECHA DE LA PUBLICACION
        $start_date = $date; //LA FECHA INICIAL QUE LE PROPORCIONAMOS A LA FUNCION
        $since_start = $start_date->diff(new \DateTime(date("Y-m-d") . " " . date("H:i:s"))); //CALCULANDO

        //SI EL VALOR YEAR O AÑO ES IGUAL A 0 ENTONCES NO HAN PASADO AÑOS O AÑO, SIGUE CON EL CONDICIONAL
        if ($since_start->y == 0) {
            //SI EL VALOR MONTH O MES ES IGUAL A 0 ENTONCES NO HAN PASADO MESES O MES, SIGUE CON EL CONDICIONAL
            if ($since_start->m == 0) {
                //SI EL VALOR DAY O DIA ES IGUAL A 0 ENTONCES NO HAN PASADO DIAS O DIA, SIGUE CON EL CONDICIONAL
                if ($since_start->d == 0) {
                    //SI EL VALOR HOURS O HORAS ES IGUAL A 0 ENTONCES NO HAN PASADO HORAS O HORA, SIGUE CON EL CONDICIONAL
                    if ($since_start->h == 0) {
                        //SI EL VALOR I O MINUTOS ES IGUAL A 0 ENTONCES NO HAN PASADO MINUTOS O MINUTO, SIGUE CON EL CONDICIONAL
                        //LE PUSIERON I PARA DIFERENCIAR LA M DE MINUTES DE LA M DE MONTHS
                        if ($since_start->i == 0) {
                            //SI EL VALOR SECONDS O SEGUNDOS ES IGUAL A 0 ENTONCES NO HAN PASADO SEGUNDOS O SEGUNDO, SIGUE CON EL CONDICIONAL
                            if ($since_start->s == 0) {
                                //SI SECONDS ES IGUAL A 0 EMPEZAMOS A CONTAR LOS SEGUNDOS
                                $result = $since_start->s . ' segundos';
                            } else {
                                //
                                if ($since_start->s == 1) {
                                    //SI SECONDS ES IGUAL A 1 ENTONCES HA PASADO 1 SEGUNDO
                                    $result = $since_start->s . ' segundo';
                                } else {
                                    //SI ES DIFERENTE DE 0 Y 1 ENTONCES HAN PASADO X SEGUNDOS
                                    $result = $since_start->s . ' segundos';
                                }
                            }
                        } else {
                            if ($since_start->i == 1) {
                                //SI I ES IGUAL A 1 ENTONCES HA PASADO 1 MINUTO
                                $result = $since_start->i . ' minuto';
                            } else {
                                //SI ES DIFERENTE DE 0 Y 1 ENTONCES HAN PASADO X MINUTOS
                                $result = $since_start->i . ' minutos';
                            }
                        }
                    } else {
                        if ($since_start->h == 1) {
                            //SI HOUR ES IGUAL A 1 ENTONCES HA PASADO 1 HORA
                            $result = $since_start->h . ' hora';
                        } else {
                            //SI ES DIFERENTE DE 0 Y 1 ENTONCES HAN PASADO X HORAS
                            $result = $since_start->h . ' horas';
                        }
                    }
                } else {
                    if ($since_start->d == 1) {
                        //SI DAY ES IGUAL A 1 ENTONCES HA PASADO 1 DIA
                        $result = $since_start->d . ' día';
                    } else {
                        //SI ES DIFERENTE DE 0 Y 1 ENTONCES HAN PASADO X DIAS
                        $result = $since_start->d . ' días';
                    }
                }
            } else {
                if ($since_start->m == 1) {
                    //SI MONTH ES IGUAL A 1 ENTONCES HA PASADO 1 MES
                    $result = $since_start->m . ' mes';
                } else {
                    //SI ES DIFERENTE DE 0 Y 1 ENTONCES HAN PASADO X MESES
                    $result = $since_start->m . ' meses';
                }
            }
        } else {
            //SI HA YEAR ES IGUAL A 1 ENTONCES HA PASADO 1 AÑO
            if ($since_start->y == 1) {
                $result = $since_start->y . ' año';
            } else {
                //SI ES DIFERENTE DE 0 Y 1 ENTONCES HAN PASADO X AÑOS
                $result = $since_start->y . ' años';
            }
        }

        //DEVOLVIENDO EL STRING CON LA FECHA FORMATEADA
        return "Hace " . $result;
    }

    public function getName(){

        return 'long_time_extension';

    }

}