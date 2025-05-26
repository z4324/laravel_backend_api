namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model;

class Huesped extends Model
{
    protected $collection = 'huespedes';
    protected $fillable = ['nombre', 'correo', 'telefono', 'condominio_id', 'fecha_registro'];
}