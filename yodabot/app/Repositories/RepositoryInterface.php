<?php

namespace App\Repositories;

interface RepositoryInterface {
    /**
     * Pre: --
     * Post: Retorna un json amb tota la informació.
     */
    public static function all();

    /**
     * Pre: La data ha d'estar disponible per fer un createAll()
     * Post: Es crea un nou registre. Es retorna la informació creada amb el nou id.
     */
    public static function create(array $data);

    /**
     * Pre: $id existeix a la taula sobre la qual s'estigui operant.
     * Post: s'actualitza la línia $id amb $data. Retorna l'objecte actualitzat. Si no el troba, no retorna res.
     */
    public static function update(array $data, $id);

    /**
     * Pre: $id existeix a la taula sobre la qual s'estigui operant.
     * Post: s'elimina o s'invalida el registre corresponent a id==$id. No retorna res.
     */
    public static function delete($id);

    /**
     * Pre: $id existeix. Sinó no retornarà res.
     * Post: Retorna el json amb id==$id.
     */
    public static function find($id);

}
