<?php
require_once "gestorBBDD.php";

class CajeroController
{
	private PDO $db;

	public function __construct()
	{
		$gestorBBDD = GestorBBDD::getInstance();
		$this->db = $gestorBBDD->getConnection();
	}
	public function obtenerSaldo(int $idCuenta){
		$stmt=$this->db->prepare("SELECT saldo 
		FROM cuentas 
		WHERE id_cuenta=:id_cuenta");
		$stmt->bindParam(":id_cuenta",$idCuenta,PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	/*public function retirarEfectivo(int $idCuenta,float|int $cantidad){
		if(!isset($idCuenta)||$idCuenta<=0){
			throw new Exception("ID de cuenta no existe o id negativo");
		}else{
		if($cantidad<=0){
			throw new Exception("Cantidad de efectivo 0 o negativo");
		}else{
			try{
				$this->db->beginTransaction();
				$saldo=$this->obtenerSaldo($idCuenta);
				if($cantidad>$saldo){
					throw new Exception("Saldo insuficiente para retirar esa cantidad");
				}
				else{
					
				}
			}catch(PDOException $e){
				return["error"=>$e->getMessage()];
			}

		}
	}
	}
	public function retirarEfectivo(int $id_cuenta, float|int $cantidad)
    {

        if ($cantidad <= 0) {
            return ["ERROR" => "Retirada valor 0 o negativo"];
        } else {
            try {
                $this->db->beginTransaction();

                $sqlActualizar = "UPDATE cuentas SET saldo=saldo-:cantidad WHERE id_cuenta = :id AND (saldo -:cantidad) >= 0 "; //Ó (saldo>=:cantidad)
                //$sql = "SELECT id_cuenta, saldo FROM cuentas WHERE id_cuenta = :id_cuenta AND saldo >= :cantidad";
                $stmt = $this->db->prepare($sqlActualizar);
                //$stmt-> execute([":id_cuenta" => $id_cuenta, ":cantidad" => $cantidad ]);  
                $stmt->execute([":cantidad" => $cantidad, ":id" => $id_cuenta]);
                $filasAfectadas = $stmt->rowCount();

                if ($filasAfectadas <= 0) {
                    $this->db->rollBack();
                    return ["error" => "Saldo insuficiente o cuenta inexistente"];
                }

                $stmt2 = $this->db->prepare("INSERT INTO movimientos(id_cuenta,tipo_movimiento,monto) VALUES (:id,'retiro',:cantidad)");
                $stmt2->execute([":cantidad" => $cantidad, ":id" => $id_cuenta]);
                $filasAfectadas = $stmt2->rowCount();
                if ($filasAfectadas <= 0) {
                    $this->db->rollback();
                    return ["error" => "No se registró el movimiento"];
                }

                $this->db->commit();
                return ["success" => "Transacción registrada correctamente"];
            } catch (PDOException $e) {
                $this->db->rollback();
                return ["error" => $e->getMessage()];
            }
        }
    } */
	/*public function retirarEfectivo(int $id_cuenta, float|int $cantidad)
{
    if ($cantidad <= 0) {
        throw new Exception("La cantidad debe ser mayor que 0");
    }
    try {
        $this->db->beginTransaction();
        // Actualización  del saldo
        $sql = "UPDATE cuentas 
                SET saldo = saldo - :cantidad 
                WHERE id_cuenta = :id 
                  AND saldo >= :cantidad";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id_cuenta, PDO::PARAM_INT);
        $stmt->execute();
		$filasAfectadas = $stmt->rowCount();
        if ($filasAfectadas <= 0) {
            $this->db->rollBack();
            throw new Exception("Saldo insuficiente o cuenta inexistente");
        }
        // Registrar movimiento
        $sqlMov = "INSERT INTO movimientos(id_cuenta, tipo_movimiento, monto) 
                   VALUES (:id, 'retiro', :cantidad)";

        $stmt2 = $this->db->prepare($sqlMov);
        $stmt2->bindParam(":id", $id_cuenta, PDO::PARAM_INT);
        $stmt2->bindParam(":cantidad", $cantidad, PDO::PARAM_STR);
        $stmt2->execute();
		$filasAfectadas = $stmt2->rowCount();
        if ($filasAfectadas <= 0) {
            $this->db->rollBack();
            throw new Exception("No se pudo registrar el movimiento");
        }
        $this->db->commit();
        return ["success" => "Retiro realizado correctamente"];
    } catch (PDOException $e) {
        $this->db->rollBack();
        throw new Exception("Error en la base de datos: " . $e->getMessage());
    }
}*/
private function actualizarSaldoRetiro(int $idCuenta, float $cantidad): void {
    $sql = "UPDATE cuentas 
            SET saldo = saldo - :cantidad 
            WHERE id_cuenta = :id 
              AND saldo >= :cantidad";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":cantidad", $cantidad);
    $stmt->bindParam(":id", $idCuenta, PDO::PARAM_INT);
    $stmt->execute();
	$filasAfectadas = $stmt->rowCount();
    if ($filasAfectadas <= 0) {
        throw new Exception("Saldo insuficiente o cuenta inexistente");
    }
}
private function registrarMovimientoRetiro(int $idCuenta, float $cantidad): void {
    $sql = "INSERT INTO movimientos(id_cuenta, tipo_movimiento, monto)
            VALUES (:id, 'retiro', :cantidad)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $idCuenta, PDO::PARAM_INT);
    $stmt->bindParam(":cantidad", $cantidad);
    $stmt->execute();
	$filasAfectadas = $stmt->rowCount();
    if ($filasAfectadas <= 0) {
        throw new Exception("No se pudo registrar el movimiento");
    }
}
public function retirarEfectivo(int $idCuenta, float|int $cantidad): array
{
	if ($cantidad <= 0) {
            return[
				"ok"=>false,
				"error"=>"La cantidad,debe ser mayor a 0"
			];
        }
    try {
        $this->db->beginTransaction();
        // Actualizar saldo
        $this->actualizarSaldoRetiro($idCuenta, $cantidad);
        // Registrar movimiento
        $this->registrarMovimientoRetiro($idCuenta, $cantidad);
        $this->db->commit();
        return [
            "ok" => true,
            "mensaje" => "Retiro realizado correctamente"
        ];
    } catch (Exception $e) {
        $this->db->rollBack();
        return [
            "ok" => false,
            "error" => $e->getMessage()
        ];
    }
}
}
