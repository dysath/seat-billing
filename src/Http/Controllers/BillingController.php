<?PHP

namespace Denngarr\Seat\Billing\Http\Controllers;


class BillingController extends Controller {

    public function getBillingView() {
    
        return view('billing:summary');
    }
}
