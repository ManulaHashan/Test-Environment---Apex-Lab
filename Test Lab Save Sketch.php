   public function savePatientDetails(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Validation
            $validator = Validator::make($request->all(), [
                'fname' => 'required',
                'lname' => 'required',
                'years' => 'required_without_all:months,days',
                'months' => 'required_without_all:years,days',
                'days' => 'required_without_all:years,months',
                'sampleNo' => 'required',
                'gender' => 'required|in:1,2',
                'test_data' => 'required|array|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 422);
            }

            // 2. Check existing sample number
            $existingSample = DB::table('lps')
                ->where('lab_lid',  $_SESSION['lid'])
                ->where('date', date('Y-m-d'))
                ->where('sampleNo', $request->input('sampleNo'))
                ->first();

            if ($existingSample) {
                return response()->json([
                    'error' => 'Duplicate sample number',
                    'message' => 'Same sample number found. Please reset and save again.'
                ], 409);
            }

            // 3. Handle Reference
            $refId = null;
            if ($request->has('ref') && !empty($request->input('ref'))) {
                $reference = DB::table('refference')
                    ->where('lid', $_SESSION['lid'])
                    ->where('name', $request->input('ref'))
                    ->first(['idref']);

                $refId = $reference ? $reference->idref : null;
            }

            // 4. Prepare Patient Data
            $patientData = [
                'sample_no' => $request->input('sampleNo'),
                'lab_branch' => $request->input('labbranch'),
                'type' => $request->input('type'),
                'source' => $request->input('source'),
                'tpno' => $request->input('tpno'),
                'initial' => $request->input('initial'),
                'first_name' => $request->input('fname'),
                'last_name' => $request->input('lname'),
                'dob' => $request->input('dob'),
                'years' => $request->input('years', 0),
                'months' => $request->input('months', 0),
                'days' => $request->input('days', 0),
                'gender' => $request->input('gender'),
                'nic' => $request->input('nic'),
                'address' => urlencode($request->input('address')),
                'refcode' => $refId,
                'testname' => $request->input('testname'),
                'pkgname' => $request->input('pkgname'),
                'fast_time' => $request->input('fast_time'),
                'test_data' => json_encode($request->input('test_data')),
                'total_amount' => $request->input('total_amount', 0),
                'discount' => $request->input('discount', 0),
                'discount_percentage' => $request->input('discount_percentage', 0),
                'grand_total' => $request->input('grand_total', 0),
                'payment_method' => $this->resolvePaymentMethod($request),
                'paid' => $request->input('paid', 0),
                'due' => $request->input('due', 0),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // 5. Handle Test Packages
            if ($request->has('pkgname') && !empty($request->input('pkgname'))) {
                $package = DB::table('labpackages')
                    ->where('Lab_lid',  $_SESSION['lid'])
                    ->where('name', $request->input('pkgname'))
                    ->first(['idlabpackages']);

                if ($package) {
                    DB::table('invoice_has_labpackages')->insert([
                        'sno' => $request->input('sampleNo'),
                        'pcid' => $package->idlabpackages,
                        'lab_lid' =>  $_SESSION['lid']
                    ]);
                }
            }

            // 6. Save Patient
            $patientId = DB::table('patient_registrations')->insertGetId($patientData);

            // 7. Generate Sample Numbers
            $sampleNo = $this->generateSampleNumber($request);
            $this->saveLpsRecords($patientId, $sampleNo, $request);

            // 8. Handle Invoice
            $invoiceId = $this->createInvoice($patientId, $request);

            // 9. Loyalty System
            // if (config('app.credit_system')) {
            //     $this->handleLoyaltySystem($patientId, $request, $invoiceId);
            // }

            DB::commit();

            return response()->json([
                'success' => true,
                'patient_id' => $patientId,
                'invoice_id' => $invoiceId,
                'message' => 'Patient saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Patient save error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while saving patient',
                'technical' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Helper Methods
    private function generateSampleNumber($request)
    {
        $sampleNo = $request->input('sampleNo');
        $date = date('ymd');

        // Branch-specific sample number
        if ($request->has('labbranch') && !empty($request->input('labbranch'))) {
            $branchCode = explode(':', $request->input('labbranch'))[0];

            $query = DB::table('lps')
                ->where('lab_lid',  $_SESSION['lid'])
                ->where('sampleNo', 'like', $branchCode . '%');

            // if (!config('app.infinity_branch_samples')) {
            //     $query->where('date', date('Y-m-d'));
            // }

            $maxSample = $query->max(DB::raw('CAST(SUBSTRING(sampleNo FROM 3) AS UNSIGNED)'));

            return $branchCode . ($maxSample ? $maxSample + 1 : 1);
        }

        // Main lab sample number
        $maxSample = DB::table('lps')
            ->where('lab_lid',  $_SESSION['lid'])
            ->where('date', date('Y-m-d'))
            ->max(DB::raw('CAST(sampleNo AS UNSIGNED)'));

        return $maxSample ? $maxSample + 1 : $date . '01';
    }

    private function saveLpsRecords($patientId, $sampleNo, $request)
    {
        $suffixArray = ["", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M"];
        $testData = $request->input('test_data');
        $now = now();

        foreach ($testData as $index => $test) {
            $lpsData = [
                'patient_pid' => $patientId,
                'Lab_lid' =>  $_SESSION['lid'],
                'date' => date('Y-m-d'),
                'sampleNo' => $sampleNo . $suffixArray[$index],
                'arivaltime' => date('H:i:s'),
                'status' => 'pending',
                'type' => $request->input('type'),
                'refference_idref' => $request->input('refcode'),
                'fastinghours' => $request->input('fast_time'),
                'price' => $test['price'],
                'Testgroup_tgid' => $test['tgid'],
                'urgent_sample' => $test['priority'] === 'Yes' ? 1 : 0,
                'source' => $request->input('source'),
                'discount_precentage' => $request->input('discount_percentage'),
                'created_at' => $now,
                'updated_at' => $now
            ];

            $lpsId = DB::table('lps')->insertGetId($lpsData);

            // Save LPS-Test relations
            $tests = DB::table('Lab_has_test')
                ->where('Lab_lid', $_SESSION['lid'] )
                ->where('Testgroup_tgid', $test['tgid'])
                ->pluck('test_tid');

            foreach ($tests as $testId) {
                DB::table('lps_has_test')->insert([
                    'lps_lpsid' => $lpsId,
                    'test_tid' => $testId,
                    'state' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }

    private function createInvoice($patientId, $request)
    {
        $paymentStatus = $this->getPaymentStatus(
            $request->input('paid', 0),
            $request->input('grand_total', 0)
        );

        $invoiceData = [
            'lps_lpsid' => $patientId,
            'date' => date('Y-m-d'),
            'total' => $request->input('total_amount', 0),
            'gtotal' => $request->input('grand_total', 0),
            'paid' => $request->input('paid', 0),
            'status' => $paymentStatus,
            'paymentmethod' => $request->input('payment_method', 'Cash'),
             'cashier' => Session::get('user_name'),
            'remark' => $request->input('remark'),
            'source' => $request->input('source'),
            'created_at' => now(),
            'updated_at' => now()
        ];

        $invoiceId = DB::table('invoice')->insertGetId($invoiceData);

        if ($request->input('paid', 0) > 0) {
            DB::table('invoice_payments')->insert([
                'date' => date('Y-m-d'),
                'amount' => $request->input('paid'),
                'user_uid' => Session::get('uid'),
                'paymethod' => $request->input('payment_method', 'Cash'),
                'invoice_iid' => $invoiceId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return $invoiceId;
    }

    private function handleLoyaltySystem($patientId, $request, $invoiceId)
    {
        if ($request->has('loyaltyno') && !empty($request->input('loyaltyno'))) {
            // Save loyalty number
            DB::table('patient_loyalty')->updateOrInsert(
                ['patient_id' => $patientId],
                [
                    'loyalty_no' => $request->input('loyaltyno'),
                    'credits' => $request->input('credits', 0),
                    'updated_at' => now()
                ]
            );

            //Update credits if no discount
            if ($request->input('discount', 0) == 0) {
                $credits = DB::table('invoice')
                    ->where('iid', $invoiceId)
                    ->value('total') * config('app.credit_rate', 0.01);

                DB::table('patient_loyalty')
                    ->where('patient_id', $patientId)
                    ->increment('credits', $credits);
            }
        }
    }

    private function getPaymentStatus($paid, $grandTotal)
    {
        if ($paid == 0) return 'Not Paid';
        if ($paid >= $grandTotal) return 'Payment Done';
        return 'Pending Due';
    }