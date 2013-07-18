<?php 
namespace T3chnik\Verifyl4Mongolid;

use Illuminate\Hashing\HasherInterface;
use Illuminate\Auth\UserProviderInterface;
use Illuminate\Auth\UserInterface;

class VerifyUserProvider implements UserProviderInterface
{
    /**
     * The hasher implementation.
     *
     * @var Illuminate\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * Create a new database user provider.
     *
     * @param  Illuminate\Hashing\HasherInterface  $hasher
     * @param  string  $model
     * @return void
     */
    public function __construct(HasherInterface $hasher, $model)
    {
        $this->model = $model;
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return Illuminate\Auth\UserInterface|null
     */
    public function retrieveByID($identifier)
    {
        $model = $this->createModel();
        return $model::first( [ '_id' => new \MongoId( $identifier ) ] );
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return Illuminate\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {   
        // Are we checking by identifier?
        if (array_key_exists('identifier', $credentials)) {
            // Grab each val to be identifed against
            $model = $this->createModel();
            foreach (\Config::get('verify-l4-mongolid::identified_by') as $identified_by) {
                // Create a new query for each check
                $where = [];
                // Start off the query with the first identified_by value
                $where[ $identified_by ] =  $credentials['identifier'];

                // Add any other values to user has passed in
                foreach ( $credentials as $key => $value ) {
                    if (
                        !str_contains($key, 'password') &&
                        !str_contains($key, 'identifier')
                    ) {
                        $where[ $key ] = $value;
                    }
                }
                
                if ( $model::where( $where )->count() != 0) {
                    break;
                }
            }
        }
        else
        {
            // First we will add each credential element to the query as a where clause.
            // Then we can execute the query and, if we found a user, return it in a
            // Eloquent User "model" that will be utilized by the Guard instances.
            $model = $this->createModel();
            $where = [];
            foreach ($credentials as $key => $value) {
                if (!str_contains($key, 'password')) {
                    $where[ $key ] = $value;
                }
            }
        }
        
        $result = $model::where( $where );
        // Failed to find a user?
        if ( $result->count() == 0) {
            throw new UserNotFoundException('User can not be found');
        }

        return $result->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  Illuminate\Auth\UserInterface  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        $plain = $credentials['password'];
        // Is user password is valid?
        if(!$this->hasher->check($user->salt.$plain, $user->getAuthPassword())) {
            throw new UserPasswordIncorrectException('User password is incorrect');
        }

        // Valid user, but are they verified?
        if (!$user->verified) {
            throw new UserUnverifiedException('User is unverified');
        }

        // Is the user disabled?
        if ($user->disabled) {
            throw new UserDisabledException('User is disabled');
        }

        // Is the user deleted?
        if ($user->deleted_at !== NULL) {
            throw new UserDeletedException('User is deleted');
        }

        return true;
    }

    /**
     * Create a new instance of the model.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }
}

class UserNotFoundException extends \Exception {};
class UserUnverifiedException extends \Exception {};
class UserDisabledException extends \Exception {};
class UserDeletedException extends \Exception {};
class UserPasswordIncorrectException extends \Exception {};