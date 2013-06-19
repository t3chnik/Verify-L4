<?php
namespace T3chnik\Verifyl4Mongolid\Models;

class Permission extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';
    public $collection = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = array( 'name' , 'description' );

    /**
     * Roles
     *
     * @return object
     */
    public function roles()
    {
        return $this->referencesMany( 'T3chnik\Verifyl4Mongolid\Models\Role', 'roles' );
    }
    
    /**
     * 
     * @param type $roles
     */
    public function addRoles( $roles ){
        
        if( !is_array( $roles ) ){
            $roles = [ $roles ];
        }
        
        foreach( $roles as $role ){
            if( !is_object( $role ) ){
                $role = Role::first( [ '_id' => $role ] );
            }
            $this->attach( 'roles' , $role );
        }
        $this->save();
        
        foreach ( $roles as $role ) {
            if( !is_object( $role ) ){
                $role = Role::first( [ '_id' => $role ] );
            }
            $role->attach( 'permissions' , $this );
            $role->save();
        }
        
    }
    
}