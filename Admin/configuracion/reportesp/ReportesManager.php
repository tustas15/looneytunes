<?php
class Reportes {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerOpciones($filtro) {
        $sql = $this->getSqlForFilter($filtro);
        if (!$sql) {
            return ['error' => 'Filtro no válido'];
        }

        $result = $this->conn->query($sql);
        if ($result === false) {
            return ['error' => 'Error en la consulta: ' . $this->conn->error];
        }

        $options = [];
        while ($row = $result->fetch_assoc()) {
            $options[] = [
                'id' => $row['id'],
                'nombre' => $row['nombre']
            ];
        }

        return $options;
    }

    private function getSqlForFilter($filtro) {
        switch ($filtro) {
            case 'categoria':
                return "SELECT ID_CATEGORIA AS id, CATEGORIA AS nombre FROM tab_categorias WHERE ACTIVO = 1 ORDER BY CATEGORIA";
            case 'deportista':
                return "SELECT ID_DEPORTISTA AS id, CONCAT(APELLIDO_DEPO, ' ', NOMBRE_DEPO) AS nombre FROM tab_deportistas WHERE ACTIVO = 1 ORDER BY APELLIDO_DEPO, NOMBRE_DEPO";
            case 'representante':
                return "SELECT ID_REPRESENTANTE AS id, CONCAT(APELLIDO_REPRE, ' ', NOMBRE_REPRE) AS nombre FROM tab_representantes WHERE ACTIVO = 1 ORDER BY APELLIDO_REPRE, NOMBRE_REPRE";
            default:
                return null;
        }
    }
}