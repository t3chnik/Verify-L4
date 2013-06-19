<?php
namespace T3chnik\Verifyl4Mongolid\Models;

class Role extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';
    public $collection = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = array('name', 'description', 'level');

    /**
     * Users
     *
     * @return object
     */
    public function users()
    {
        return $this->referencesMany( 'T3chnik\Verifyl4Mongolid\Models\User' , 'users' );
    }
    
    /**
     * 
     * @param type $users
     */
    public function addUsers( $users ){
        
        if( !is_array( $users ) ){
            $users = [ $users ];
        }
        
        foreach( $users as $user ){
            if( !is_object( $user ) ){
                $user = User::first([ '_id' => $user ]);
            }
            $this->attach( 'users' , $user );
        }
        $this->save();
        
        foreach( $users as $user ){
            $user->attach( 'roles' , $this );
            $user->save();
        }
        
    }

    /**
     * Permissions
     *
     * @return object
     */
    public function permissions()
    {
        return $this->referencesMany( 'T3chnik\Verifyl4Mongolid\Models\Permission' , 'permissions' );
    }
    
    /*
     * 
     */
    public function addPermissions( $permissions ){
        
        if( !is_array( $permissions ) ){
            $permissions = [ $permissions ];
        } 

        foreach( $permissions as $permission ){
            if( !is_object($permission) ){
                $permission = Permission::first( [ '_id' => $permission ] );
            }
            $this->attach( 'permissions' , $permission );
            
        }
        $this->save();
        
        foreach( $permissions as $permission ){
            $permission->attach( 'roles' , $this );
            $permission->save();
        }
        
    }
    
}