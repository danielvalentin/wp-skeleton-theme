<?php

class debug
{
	
	public static function dump($var, $silent = false)
	{
		if($silent)
		{
			echo '<!--';
		}
		echo self::unravel($var);
		if($silent)
		{
			echo '-->';
		}
	}
	
	public static function unravel($obj, $start = true)
	{
		if($start)
		{
			$returner = '<ul>';
		}
		else
		{
			$returner = '';
		}
		if(is_array($obj))
		{
			if($start)
			{
				$returner .= '<li><em>Array (' . count($obj) . ')</em>:<ul>';
			}
			else
			{
				$returner .= '<ul>';
			}
			foreach($obj as $key => $val)
			{
				if(is_array($val))
				{
					$returner .= '<li><em>Array (' .  $key . ')</em>:';
					$returner .= self::unravel($val, false);
				}
				elseif(is_object($val))
				{
					$returner .= self::unravel($val);
				}
				else
				{
					$type = gettype($val);
					$returner .= '<li><strong>' . $key . '</strong> => (' . $type . ') ' . $val . '</li>';
				}
			}
			$returner .= '</li>';
		}
		elseif(is_object($obj))
		{
			$classname = get_class($obj);
			$returner .= '<li><em>Object (' . $classname . ')</em>:<ul>';
			$objVars = get_object_vars($obj);
			foreach($objVars as $key => $val)
			{
				if(is_array($val) || is_object($val))
				{
					$returner .= '<li><strong>' . $key . '</strong>: (' . ((is_object($val)) ? 'object' : 'array') . ' ' . count($val) . ')';
					$returner .= self::unravel($val, false);
				}
				else
				{
					$type = gettype($val);
					$returner .= '<li><strong>' . $key . '</strong>: (' . $type . ') ' . $val . '</li>';
				}
			}
			$methods = get_class_methods($obj);
			$reflection = new ReflectionClass($obj);
			$classMethods = $reflection -> getMethods();
			$returner .= '<li><strong style="color:brown;">Class methods</strong>:' . ((!$classMethods) ? ' <em>None defined</em>' : '') . '<ul style="margin:0;padding:0;">';
			foreach($classMethods as $m)
			{
				$returner .= '<li>';
				if($m -> isPrivate())
				{
					$returner .= '<span style="color:red;">private</span>';
				}
				if($m -> isProtected())
				{
					$returner .= '<span style="color:blue;">protected</span>';
				}
				if($m -> isPublic())
				{
					$returner .= '<span style="color:green;">public</span>';
				}
				if($m -> isStatic())
				{
					$returner .= ' <em>static</em> ';
				}
				$returner .= ' <strong>' . $m -> name . '</strong>(';
				$params = $m -> getParameters();
				$numParams = count($params);
				$i = 1;
				foreach($params as $param)
				{
					$returner .= $param -> getName();
					if($i < $numParams)
					{
						$returner .= ', ';
					}
					$i++;
				}
				$returner .= ')';
				$returner .= '</li>';
			}
			$returner .= '</ul></li>';
			if($reflection -> getFileName())
			{
				$returner .= '<li><strong>Defined in file</strong>: ' .$reflection -> getFileName() . ', <strong>at line</strong>: ' . $reflection -> getStartLine() . '</li>';
			}
			$returner .= '</ul></li>';
		}
		else
		{
			$type = ucfirst(gettype($obj));
			$returner .= '<li><em>' . $type . '</em>: ' . $obj . '</li>';
		}
		$returner .= '</ul></li>';
		return $returner;
	}
	
}
