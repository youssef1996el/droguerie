<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;

use Spatie\Permission\Models\Permission;

use DB;

use App\Models\Company;

class RoleController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    /* function __construct()

    {

         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);

         $this->middleware('permission:role-create', ['only' => ['create','store']]);

         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);

         $this->middleware('permission:role-delete', ['only' => ['destroy']]);

    } */



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $roles = Role::orderBy('id','DESC')->paginate(5);
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page role')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('roles.index',compact('roles'))

            ->with('i', ($request->input('page', 1) - 1) * 5)
            ->with('CompanyIsActive'         ,$CompanyIsActive);

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        //$permission = Permission::get();
        $permission = Permission::all()->groupBy(function ($item, $key) {
            return explode('-', $item->name)[0]; // Group by the first part of the permission name
        });
        $CountCompany          = Company::count();
        if($CountCompany == 0)
        {
            return view('Errors.index')
            ->with('title','Il n\'est pas possible d\'accéder à la page role')
            ->with('body',"Parce qu'il n'y a pas de société active");
        }
        $CompanyIsActive       = Company::where('status','Active')->select('title')->first();
        return view('roles.create',compact('permission','CompanyIsActive'));

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $this->validate($request, [

            'name' => 'required|unique:roles,name',

            'permission' => 'required',

        ]);



        $role = Role::create(['name' => $request->input('name')]);

        // Fetch permission names based on IDs
        $permissions = Permission::whereIn('id', $request->input('permission'))->pluck('name');
       /*  $role->syncPermissions($request->input('permission')); */
       $role->syncPermissions($permissions);



        return redirect()->route('roles.index')

                        ->with('success','Role created successfully');

    }

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $role = Role::find($id);

        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")

            ->where("role_has_permissions.role_id",$id)

            ->get();

            $CountCompany          = Company::count();
            if($CountCompany == 0)
            {
                return view('Errors.index')
                ->with('title','Il n\'est pas possible d\'accéder à la page role')
                ->with('body',"Parce qu'il n'y a pas de société active");
            }
            $CompanyIsActive       = Company::where('status','Active')->select('title')->first();

        return view('roles.show',compact('role','rolePermissions','CompanyIsActive'));

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $role = Role::find($id);

        //$permission = Permission::get();
        $permission = Permission::all()->groupBy(function ($item, $key) {
            return explode('-', $item->name)[0]; // Group by the first part of the permission name
        });
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)

            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')

            ->all();
            $CountCompany          = Company::count();
            if($CountCompany == 0)
            {
                return view('Errors.index')
                ->with('title','Il n\'est pas possible d\'accéder à la page role')
                ->with('body',"Parce qu'il n'y a pas de société active");
            }
            $CompanyIsActive       = Company::where('status','Active')->select('title')->first();


        return view('roles.edit',compact('role','permission','rolePermissions','CompanyIsActive'));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        $this->validate($request, [

            'name' => 'required',

            'permission' => 'required',

        ]);



        $role = Role::find($id);

        $role->name = $request->input('name');

        $role->save();


        $permissions = Permission::whereIn('id', $request->input('permission'))->pluck('name');
      /*   $role->syncPermissions($request->input('permission')); */

      $role->syncPermissions($permissions);

        return redirect()->route('roles.index')

                        ->with('success','Role updated successfully');

    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        DB::table("roles")->where('id',$id)->delete();

        return redirect()->route('roles.index')

                        ->with('success','Role deleted successfully');

    }

}
