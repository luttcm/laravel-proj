<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Variable;

class VariableController extends Controller
{
    public function index()
    {
        $companyVariables = Variable::where('table_type', 'company')->paginate(10, ['*'], 'company_page');
        $fncVariables = Variable::where('table_type', 'fnc')->paginate(10, ['*'], 'fnc_page');
        return view("pages.variables", compact("companyVariables", "fncVariables"));
    }

    public function add()
    {
        $types = ["float", "integer"];
        return view("pages.variable-add", compact("types"));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:float,integer',
            'table_type' => 'required|string|in:company,fnc',
            'value' => 'required|string|min:1',
        ]);

        $value = trim($validated['value']);
        $type = $validated['type'];

        if ($type === 'integer') {
            if (strpos((string)$value, ',') == true) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Используйте точку в качестве разделителя десятичных чисел');
            }
            if (!is_numeric($value)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Целое число: введите число без букв');
            }
            $value = (int)$value;
        } else {
            if (strpos((string)$value, ',') == true) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Используйте точку в качестве разделителя десятичных чисел');
            }
            if (!is_numeric($value)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Дробное число: введите число без букв');
            }
            $value = (float)$value;
            if (strpos((string)$value, '.') === false) {
                $value = $value . '.0';
            }
        }

        $user = Variable::create([
            'name' => $validated['name'],
            'value' => (string)$value,
            'type' => $type,
            'table_type' => $validated['table_type'],
        ]);

        return redirect()->route('variables.index')
            ->with('success', "Переменная создана!");
    }

    public function edit($id)
    {
        $variable = Variable::findOrFail($id);
        $types = ["float", "integer"];
        return view('pages.variable-edit', compact('variable', 'types'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:float,integer',
            'table_type' => 'required|string|in:company,fnc',
            'value' => 'required|string|min:1',
        ]);

        $value = trim($validated['value']);
        $type = $validated['type'];

        if ($type === 'integer') {
            if (strpos((string)$value, ',') == true) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Используйте точку в качестве разделителя десятичных чисел');
            }
            if (!is_numeric($value)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Целое число: введите число без букв');
            }
            $value = (int)$value;
        } else {
            if (strpos((string)$value, ',') == true) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Используйте точку в качестве разделителя десятичных чисел');
            }
            if (!is_numeric($value)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Дробное число: введите число без букв');
            }
            $value = (float)$value;
            if (strpos((string)$value, '.') === false) {
                $value = $value . '.0';
            }
        }

        $variable = Variable::findOrFail($id);
        $variable->name = $validated['name'];
        $variable->type = $type;
        $variable->table_type = $validated['table_type'];
        $variable->value = (string)$value;
        $variable->save();

        return redirect()->route('variables.index')->with('success', 'Переменная обновлена');
    }

    public function delete($id)
    {
        $variable = Variable::findOrFail($id);
        $variable->delete();
        return redirect()->route('variables.index')->with('success', 'Переменная удалена');
    }
}
