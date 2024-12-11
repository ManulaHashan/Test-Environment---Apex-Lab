

<?php

use Illuminate\Http\RedirectResponse;

class PagesController extends Controller
{

    public function add()
    {
        $people = ['Sama', 'Ara', 'Laka'];
        return View::make('addrecept', ['people' => $people]);
    }

    public function update()
    {
        return "update page";
    }

    public function manageRecept()
    {
        $from = Input::get('from');
        $amount = Input::get('amount');
        $reason = Input::get('reason');
        $date = Input::get('date');
        $method = Input::get('method');

        $message = "";

        if (Input::get('submit') == 'Save Recept') {

            if (Recepts::where('customer', '=', $from)
                ->where('amount', '=', $amount)->exists()
            ) {
                $message = "0";
            } else {

                $message = DB::table('recepts')->insert(['customer' => $from, 'amount' => $amount, 'reason' => $reason, 'date' => $date, 'method' => $method]);
            }
        } elseif (Input::get('submit') == 'Update Recept') {



            $message = DB::table('recepts')
                ->where('rid', Input::get('id'))
                ->update(['customer' => $from, 'amount' => $amount, 'reason' => $reason, 'date' => $date, 'method' => $method]);
        } elseif (Input::get('submit') == 'Delete Recept') {
            $message = DB::table('recepts')->where('rid', Input::get('id'))->delete();
        }

        if ($message == 1) {
            $message = "Oparation Done!";
        } else {
            $message = "Oparation Error!";
        }



        //return View::make('addrecept')
        //->with('message',$message)
        //->with('recepts',Recepts::all());
        //return redirect('addrecept')->with('status', 'Profile updated!');

        return redirect('addrecept');
        //->with('message',$message)
        //->with('recepts',Recepts::all());
    }

    public function SearchRecept()
    {
        $customer = Input::get('customer');
        $amount = Input::get('amount');

        if ($amount == "") {
            $amount = "%";
        }

        $query = "select * from recepts where customer like '" . $customer . "' and amount like '" . $amount . "'";

        $result = DB::select($query);

        //$out = "<tr><td>Customer</td><td>Amount</td><td>Reason</td><td>Date</td><td>Method</td></tr>";
        //foreach ($result as $key) {
        //$out = $out . "<tr><td>".$key->customer."</td> <td>".$key->amount."</td> <td>".$key->reason."</td> <td>".$key->date."</td> <td>".$key->method."</td></tr>";
        //}
        //return $result;

        return json_encode($result);
    }
}
